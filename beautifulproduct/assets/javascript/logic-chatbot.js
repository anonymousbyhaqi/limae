function getBotReply(userMessage) {
  const message = userMessage.toLowerCase();

  // RULE-BASED RESPONSE
  for (let rule of chatbotRules) {
    for (let keyword of rule.keywords) {
      if (message.includes(keyword)) {
        return rule.response;
      }
    }
  }

  // DEFAULT SMART RESPONSE
  return "Maaf 🙏 aku belum memahami pertanyaan itu. Kamu bisa bertanya tentang jenis kulit, produk, atau harga 💖";
}

