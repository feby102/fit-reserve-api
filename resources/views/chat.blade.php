<!DOCTYPE html>
<html>
<head>
    <title>Real-time Chat</title>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>

    <style>
        body { font-family: Arial; }
        #messages { border:1px solid #ccc; height:300px; overflow:auto; padding:10px; }
        input { width: 80%; padding: 10px; }
        button { padding: 10px; }
    </style>
</head>
<body>

<h2>🔥 Chat</h2>

<div id="messages"></div>

<input type="text" id="msg" placeholder="اكتب رسالة...">
<button onclick="sendMessage()">Send</button>

<script>
    // 🔹 Pusher setup مع PrivateChannel + auth
    const token = "3|VpJDFdZonZywk5nljbwKaXc8oIhXqtJxAmxfeR9Ue27c5354"; // التوكن الصحيح

    const pusher = new Pusher("367f7b064b0aa7cc55b3", {
        cluster: "eu",
        forceTLS: true, // localhost بدون https
        authEndpoint: "http://127.0.0.1:8000/broadcasting/auth", // endpoint auth
        auth: {
            headers: {
                Authorization: "Bearer " + token
            }
        }
    });

    // Debug connection
    pusher.connection.bind('connected', function() {
        console.log("✅ Connected to Pusher");
    });

    pusher.connection.bind('error', function(err) {
        console.error("❌ Pusher Error:", err);
    });

    // الاشتراك في PrivateChannel
    // const channel = pusher.subscribe("private-chat.1"); // لاحظ private
const channel = pusher.subscribe("chat.1"); // قناة عامة
    channel.bind("pusher:subscription_succeeded", function() {
        console.log("✅ Subscribed to private-chat.1");
    });

    // استلام الرسائل
    channel.bind("message.sent", function(data) {
        console.log("🔥 MESSAGE RECEIVED:", data);
        let box = document.getElementById("messages");
        box.innerHTML += "<p><b>" + data.message.sender.name + ":</b> " + data.message.message + "</p>";
    });

    // إرسال الرسائل
    function sendMessage() {
        let message = document.getElementById("msg").value;

        fetch("http://127.0.0.1:8000/api/chat/send", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            },
            body: JSON.stringify({
                conversation_id: 1,
                message: message,
                type: "text"
            })
        }).then(res => res.json())
          .then(data => console.log("Message sent:", data))
          .catch(err => console.error("Send error:", err));

        document.getElementById("msg").value = "";
    }
</script>

</body>
</html>