let chatMessages; // ✅ global

document.addEventListener("DOMContentLoaded", () => {
  const chatToggle = document.getElementById("chatToggle");
  const chatBox = document.getElementById("chatBox");
  const sendBtn = document.getElementById("sendBtn");
  const chatInput = document.getElementById("chatInput");
  chatMessages = document.getElementById("chatMessages");

  // Toggle chatbot
  chatToggle.addEventListener("click", (e) => {
    e.stopPropagation();
    chatBox.classList.toggle("opacity-0");
    chatBox.classList.toggle("scale-95");
    chatBox.classList.toggle("pointer-events-none");
  });

  // Klik luar → tutup
  document.addEventListener("mousedown", (e) => {
    if (!chatBox.contains(e.target) && !chatToggle.contains(e.target)) {
      chatBox.classList.add("opacity-0", "scale-95", "pointer-events-none");
    }
  });

  // Fokus input → tetap terbuka
  chatInput.addEventListener("focus", () => {
    chatBox.classList.remove("opacity-0", "scale-95", "pointer-events-none");
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

    // Bubble mengetik
    showTypingBubble();

    setTimeout(() => {
      // hapus bubble
      const typingBubble = document.getElementById("typingBubble");
      if (typingBubble) typingBubble.remove();

      // Pesan bot
      const botMsg = document.createElement("div");
      botMsg.className =
        "bg-pink-100 text-gray-800 p-3 rounded-xl w-fit text-left";

      botMsg.textContent = getBotReply(text);

      chatMessages.appendChild(botMsg);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }, 1200);
  }

  sendBtn.addEventListener("click", sendMessage);
  chatInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") sendMessage();
  });
});

// Bubble typing
function showTypingBubble() {
  const typing = document.createElement("div");
  typing.id = "typingBubble";
  typing.className =
    "bg-pink-100 text-gray-600 p-3 rounded-xl w-fit text-sm italic";

  typing.innerHTML = `
    BeautyCare sedang mengetik
    <span class="dot">.</span><span class="dot">.</span><span class="dot">.</span>
  `;

  chatMessages.appendChild(typing);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}


