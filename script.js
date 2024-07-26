document.getElementById("chatForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const userMessage = document.getElementById("userMessage").value;
  const chatHistory = document.getElementById("chatHistory");

  chatHistory.innerHTML += `<p><strong>User: </strong> ${userMessage}</p>`;
  document.getElementById("userMessage").value = "";

  fetch("index.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "message=" + encodeURIComponent(userMessage),
  })
    .then((response) => {
      console.log("Raw response:", response);
      return response.text(); // Read response as text for debugging
    })
    .then((text) => {
      console.log("Response text:", text); // Log the raw text response
      return JSON.parse(text); // Attempt to parse it as JSON
    })
    .then((data) => {
      if (data.message) {
        chatHistory.innerHTML += `<p><strong>Bot:</strong> ${data.message}</p>`;
      } else {
        chatHistory.innerHTML += `<p><strong>Error:</strong> ${data.error}</p>`;
      }
      chatHistory.scrollTop = chatHistory.scrollHeight;
    })
    .catch((error) => {
      chatHistory.innerHTML += `<p>Error: ${error.message}</p>`;
      console.error("Fetch error:", error);
      chatHistory.scrollTop = chatHistory.scrollHeight;
    });
});
