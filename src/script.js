// Saving the session data in a variable if logged in or false if not
const isLogged = user ? true : false;

// Importing the config file
const configResponse = await fetch("./config.json");
const config = await configResponse.json();

// Requesting permission to send notifications
await Notification.requestPermission();

// Variable to keep track of unread messages
let unreadMessagesCount = 0;

// Create WebSocket connection.
const socket = new WebSocket(config.wss);

// Global variables
const form = document.querySelector("form");
const connectedCount = document.querySelector("#connected");

// Function to render message
const RenderMessage = (message_id, id, name, message, timestamp) => {
    // Format the timestamp to a readable time "HH:MM:SS"
    const formattedTime = new Date(Number(timestamp)).toLocaleTimeString();

    // Create message container with the message_id as a data attribute
    const messageContainer = document.createElement("div");
    messageContainer.classList.add("message_container");
    messageContainer.dataset.message_id = message_id;

    // Check if message is from self
    if (id === user["id"]) {
        messageContainer.classList.add("self");
    }

    // Create message
    const messageElement = document.createElement("div");
    messageElement.classList.add("message");

    // Create message user name
    const nameElement = document.createElement("div");
    nameElement.classList.add("message__name");
    nameElement.innerText = name;

    // Create message text
    const textElement = document.createElement("div");
    textElement.classList.add("message__text");
    textElement.innerText = message;

    // Create message time
    const timeElement = document.createElement("div");
    timeElement.classList.add("message__time");
    timeElement.innerText = formattedTime;

    // Append elements to message & message container and append to DOM
    messageElement.append(nameElement, textElement, timeElement);
    messageContainer.append(messageElement);
    document.querySelector(".messages").append(messageContainer);
};

// Render messages from database on page load
isLogged &&
    fetch(config.getMessages)
        .then((response) => response.json())
        .then((data) => {
            // Check if there is an error
            if (data.status === "error") {
                alert(data.message);
                throw new Error(data.message);
            }

            // Render messages
            data.forEach((message) => {
                RenderMessage(
                    message["id"],
                    message["user_id"],
                    message["user_name"],
                    message["message"],
                    message["created_at"]
                );
            });

            // Scroll to bottom of messages
            window.scrollTo(0, document.body.scrollHeight);
        });

// Connection opened
isLogged &&
    socket.addEventListener("open", (event) => {
        const connecting_loading = document.querySelector(
            "#connecting_loading"
        );

        // Decrease the opacity of the connecting loading
        connecting_loading.style.opacity = 0;

        // remove the "show" class from it
        setTimeout(() => {
            connecting_loading.classList.remove("show");
        }, 500);

        // Send connection data to server
        socket.send(
            JSON.stringify({
                type: "connection",
                content: {
                    id: user["id"],
                    name: user["name"],
                    created_at: user["created_at"],
                    image: user["image"],
                },
            })
        );
    });

// Send message on form submit
form.addEventListener("submit", (event) => {
    // Prevent page reload (default form behavior)
    event.preventDefault();

    // Regedit for empty messages
    if (!form.elements.message.value.trim()) {
        alert("Message is empty");
        throw new Error("Message is empty");
    }

    // Regedit for messages than are only spaces
    if (!form.elements.message.value.trim().replace(/\s/g, "").length) {
        alert("Message can't be only spaces");
        throw new Error("Message can't be only spaces");
    }

    // Create data object
    const dataObject = {
        id: user["id"],
        name: user["name"],
        message: form.elements.message.value.trim(),
        timestamp: Date.now(),
    };

    // Send message to database
    fetch(config.insertMessage, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(dataObject),
    })
        .then((response) => response.json())
        .then((data) => {
            // Check if there is an error and throw it
            if (data.status !== "success") {
                alert(data.message);
                throw new Error(data.message);
            }

            // Add the message_id to the dataObject
            dataObject["message_id"] = data.message_id;

            // Send message to server
            socket.send(
                JSON.stringify({
                    type: "message",
                    content: dataObject,
                })
            );
        });

    // Clear the message input
    form.elements.message.value = "";
});

// Listen for messages
isLogged &&
    socket.addEventListener("message", (event) => {
        // Check if the message is a JSON object
        if (event.data) {
            // Parse the JSON object
            const data = JSON.parse(event.data);

            // Check if the data is a message
            if (data.type === "message") {
                // Render the message
                RenderMessage(
                    data.content.message_id,
                    data.content.id,
                    data.content.name,
                    data.content.message,
                    data.content.timestamp
                );

                // If the user is on a different tab
                if (!document.hasFocus()) {
                    // Increment the unread messages count and change the title
                    document.title = `(${++unreadMessagesCount}) Chat`;

                    // Send a notification
                    new Notification("New message from " + data.content.name, {
                        body: data.content.message,
                    });
                }

                // Check if user came back to the page
                document.addEventListener("visibilitychange", () => {
                    if (document.visibilityState === "visible") {
                        document.title = "Chat";
                        unreadMessagesCount = 0;
                    }
                });

                // Scroll to bottom of messages
                window.scrollTo(0, document.body.scrollHeight);
            }

            if (data.type === "connected_users") {
                // Update connected users count and reset the users container
                connectedCount.innerText = data.content.length;
                const connectedUsers = document.querySelector("#users");
                connectedUsers.innerHTML = "";
                data.content.forEach((user) => {
                    // Create user container
                    const userContainer = document.createElement("div");
                    userContainer.classList.add("user");

                    // Create user image
                    const userImage = document.createElement("img");
                    userImage.classList.add("user__image");

                    if (user["image"] !== null) {
                        userImage.src = "image.php?filename=" + user["image"];
                    } else {
                        userImage.src = "./public/images/default.jpg";
                    }

                    // Create user name
                    const userName = document.createElement("div");
                    userName.classList.add("user__name");
                    userName.innerText = user["name"];

                    // Create user status
                    const userStatus = document.createElement("div");
                    userStatus.classList.add("user__status");

                    // Create user status icon
                    const userStatusIcon = document.createElement("i");
                    userStatusIcon.classList.add("fa-solid", "fa-circle");

                    // Append elements to user status and user container
                    userStatus.append(userStatusIcon);
                    userContainer.append(userImage, userName, userStatus);

                    // Append user container to DOM
                    connectedUsers.append(userContainer);
                });
            }
        }
    });

// On window close send the ID of the user to the server
window.addEventListener("unload", (event) => {
    // Send disconnection data to server
    socket.send(
        JSON.stringify({
            type: "disconnection",
            content: {
                id: user["id"],
            },
        })
    );

    // Close the socket
    socket.close();
});

// Opening/Closing the connected users menu
const connectedUsersBtn = document.querySelector("#connectedUsers__btn");
const connectedUsers = document.querySelector("#connectedUsers");
const close_connected_menu = document.querySelector("#close_connected_menu");

// Toggle the connected users menu by clicking on the button from the header
connectedUsersBtn.addEventListener("click", () => {
    connectedUsers.classList.toggle("show");
});

// Toggle the connected users menu by clicking on the close button (X)
close_connected_menu.addEventListener("click", () => {
    if (connectedUsers.classList.contains("show")) {
        connectedUsers.classList.remove("show");
    }
});
