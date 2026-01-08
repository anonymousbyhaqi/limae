
document.addEventListener("DOMContentLoaded", () => {
  const chatToggle = document.getElementById("chatToggle");
  const chatBox = document.getElementById("chatBox");
  const sendBtn = document.getElementById("sendBtn");
  const chatInput = document.getElementById("chatInput");
  const chatMessages = document.getElementById("chatMessages");

  // Toggle chatbot
  chatToggle.addEventListener("click", (e) => {
    e.stopPropagation();
    chatBox.classList.toggle("opacity-0");
    chatBox.classList.toggle("scale-95");
    chatBox.classList.toggle("pointer-events-none");
  });

  // Klik luar → tutup chatbot
  document.addEventListener("click", (e) => {
  if (!chatBox.contains(e.target) && !chatToggle.contains(e.target)) {
    chatBox.classList.add("scale-95", "opacity-0", "pointer-events-none");
  }
});

  chatBox.addEventListener("click", (e) => {
  e.stopPropagation();
});


  // Kirim pesan
  function sendMessage() {
    const text = chatInput.value.trim();
    if (!text) return;

    // Pesan user
    const userMsg = document.createElement("div");
    userMsg.className =
      "bg-pink-500 text-white p-3 rounded-xl w-fit ml-auto";
    userMsg.textContent = text;
    chatMessages.appendChild(userMsg);

    chatInput.value = "";
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Balasan bot
    setTimeout(() => {
      const botMsg = document.createElement("div");
      botMsg.className =
        "bg-pink-100 text-gray-800 p-3 rounded-xl w-fit text-left";
      botMsg.textContent = getBotReply(text);
      chatMessages.appendChild(botMsg);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }, 600);
  }

  sendBtn.addEventListener("click", sendMessage);

  chatInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") sendMessage();
  });
});
