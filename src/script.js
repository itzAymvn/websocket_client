// Saving the session data in a variable if logged in or false if not
const isLogged = user ? true : false;

// Importing the config file
import config from "./config.json" assert {type: "json"};

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
        socket.send(
            JSON.stringify({
                type: "connection",
                content: {
                    id: user["id"],
                    name: user["name"],
                    created_at: user["created_at"],
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
                connectedCount.innerText = data.content;
            }
        }
    });

// On window close send the ID of the user to the server
window.addEventListener("unload", (event) => {
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
