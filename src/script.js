const isLogged = user ? true : false;
import config from "./config.json" assert {type: "json"};
    
// Create WebSocket connection.
const socket = new WebSocket(config.wss);
const form = document.querySelector("form");

// Function to render message
const RenderMessage = (id, name, message, timestamp) => {
    const formattedTime = new Date(Number(timestamp)).toLocaleTimeString();

    // Create message container
    const messageContainer = document.createElement("div");
    messageContainer.classList.add("message_container");

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
            data.forEach((message) => {
                RenderMessage(
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
    event.preventDefault();
    // Regedit for empty messages
    if (!form.elements.message.value.trim()) {
        throw new Error("Message is empty");
    }

    // Regedit for messages than are only spaces
    if (!form.elements.message.value.trim().replace(/\s/g, "").length) {
        throw new Error("Message can't be only spaces");
    }

    const dataObject = {
        id: user["id"],
        name: user["name"],
        message: form.elements.message.value.trim(),
        timestamp: Date.now(),
    };

    fetch(config.insertMessage, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },

        body: JSON.stringify(dataObject),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status !== "success") {
                alert(data.message);
                throw new Error(data.message);
            }

            // Send message to server
            socket.send(
                JSON.stringify({
                    type: "message",
                    content: dataObject,
                })
            );
        });

    form.elements.message.value = "";
});

// Listen for messages
isLogged &&
    socket.addEventListener("message", (event) => {
        if (event.data) {
            const data = JSON.parse(event.data);
            if (data.type === "message") {
                RenderMessage(
                    data.content.id,
                    data.content.name,
                    data.content.message,
                    data.content.timestamp
                );

                // Scroll to bottom of messages
                window.scrollTo(0, document.body.scrollHeight);
            }
        }
    });

// On window close send the ID of the user to the server
window.addEventListener("beforeunload", (event) => {
    socket.send(
        JSON.stringify({
            type: "disconnection",
            content: {
                id: user["id"],
            },
        })
    );

    socket.close();
});
