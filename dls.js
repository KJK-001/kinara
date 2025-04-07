<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dream League Soccer</title>
  <!-- Firebase SDKs -->
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth.js"></script>
  <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-firestore.js"></script>

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f7f7;
      text-align: center;
    }
    #auth-container button {
      margin: 10px;
      padding: 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
    }
    button:hover {
      background-color: #45a049;
    }
    h2 {
      color: #333;
    }
    .hidden {
      display: none;
    }
  </style>
</head>
<body>
  <div id="app">
    <div id="auth-container">
      <button id="sign-in-btn">Sign In</button>
      <button id="sign-out-btn" class="hidden">Sign Out</button>
    </div>

    <div id="profile-section" class="hidden">
      <h2>Welcome to Your Team!</h2>
      <p id="user-name"></p>
      <p>Coins: <span id="user-coins"></span></p>
      <button id="create-team-btn">Create Team</button>
      <ul id="teams-list"></ul>
    </div>

    <div id="game-section" class="hidden">
      <h2>Multiplayer Match</h2>
      <button id="join-game-btn">Join Game</button>
      <p id="waiting-msg" class="hidden">Waiting for opponent...</p>
      <button id="start-game-btn" class="hidden">Start Game</button>
    </div>

    <div id="chat-section" class="hidden">
      <h2>Game Chat</h2>
      <ul id="chat-messages"></ul>
      <input type="text" id="chat-input" placeholder="Type your message">
      <button id="send-chat-btn">Send</button>
    </div>
  </div>

  <script>
    // Firebase Config
    const firebaseConfig = {
      apiKey: "AIzaSyDLmsdKfv4vK8FP5x2BZg4E2Ps78Q0x2ms",
      authDomain: "game-connect-38236.firebaseapp.com",
      projectId: "game-connect-38236",
      storageBucket: "game-connect-38236.appspot.com",
      messagingSenderId: "190049119694",
      appId: "1:190049119694:web:c305cd33933c240cad4f9f",
      measurementId: "G-1EK0R8L0MG"
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
    const auth = firebase.auth();
    const firestore = firebase.firestore();

    const signInBtn = document.getElementById('sign-in-btn');
    const signOutBtn = document.getElementById('sign-out-btn');
    const createTeamBtn = document.getElementById('create-team-btn');
    const joinGameBtn = document.getElementById('join-game-btn');
    const startGameBtn = document.getElementById('start-game-btn');
    const sendChatBtn = document.getElementById('send-chat-btn');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const waitingMsg = document.getElementById('waiting-msg');
    const profileSection = document.getElementById('profile-section');
    const gameSection = document.getElementById('game-section');
    const teamsList = document.getElementById('teams-list');

    // Authentication
    signInBtn.addEventListener('click', () => {
      auth.signInAnonymously().then(() => {
        toggleAuthState();
      });
    });

    signOutBtn.addEventListener('click', () => {
      auth.signOut().then(() => {
        toggleAuthState();
      });
    });

    function toggleAuthState() {
      if (auth.currentUser) {
        signInBtn.style.display = 'none';
        signOutBtn.style.display = 'inline-block';
        loadUserData();
      } else {
        signInBtn.style.display = 'inline-block';
        signOutBtn.style.display = 'none';
        profileSection.classList.add('hidden');
        gameSection.classList.add('hidden');
      }
    }

    // Load user data
    function loadUserData() {
      const userId = auth.currentUser.uid;
      firestore.collection('Users').doc(userId).get().then(doc => {
        const userData = doc.data();
        document.getElementById('user-name').textContent = `Hello, ${userData.name}`;
        document.getElementById('user-coins').textContent = userData.coins;
        profileSection.classList.remove('hidden');
        loadUserTeams();
      });
    }

    // Create team
    createTeamBtn.addEventListener('click', () => {
      const userId = auth.currentUser.uid;
      const teamName = prompt('Enter your team name');
      if (teamName) {
        firestore.collection('Teams').add({
          name: teamName,
          owner: userId,
          members: [userId],
          coins: 0
        }).then(() => {
          alert('Team created!');
          loadUserTeams();
        });
      }
    });

    // Load teams
    function loadUserTeams() {
      const userId = auth.currentUser.uid;
      firestore.collection('Teams').where('owner', '==', userId).get().then(snapshot => {
        teamsList.innerHTML = '';
        snapshot.forEach(doc => {
          const team = doc.data();
          const li = document.createElement('li');
          li.textContent = team.name;
          teamsList.appendChild(li);
        });
      });
    }

    // Multiplayer matchmaking
    let gameRoomId = null;
    joinGameBtn.addEventListener('click', () => {
      const userId = auth.currentUser.uid;
      firestore.collection('Games').add({
        player1: userId,
        player2: null,
        status: 'waiting'
      }).then(gameRoom => {
        gameRoomId = gameRoom.id;
        listenForOpponent(gameRoom.id);
      });
    });

    // Listen for opponent
    function listenForOpponent(roomId) {
      firestore.collection('Games').doc(roomId).onSnapshot(doc => {
        const data = doc.data();
        if (data.player2) {
          waitingMsg.classList.add('hidden');
          startGameBtn.classList.remove('hidden');
        }
      });
    }

    // Start game
    startGameBtn.addEventListener('click', () => {
      alert('Game Started!');
      // Your game logic goes here (match starts, player actions, etc.)
    });

    // Chat functionality
    sendChatBtn.addEventListener('click', () => {
      const message = chatInput.value;
      if (message) {
        const userId = auth.currentUser.uid;
        firestore.collection('Games').doc(gameRoomId).collection('chat').add({
          sender: userId,
          message: message,
          timestamp: firebase.firestore.FieldValue.serverTimestamp()
        });
        chatInput.value = '';
      }
    });

    // Listen to chat messages
    function loadChatMessages() {
      firestore.collection('Games').doc(gameRoomId).collection('chat')
        .orderBy('timestamp')
        .onSnapshot(snapshot => {
          snapshot.docs.forEach(doc => {
            const msg = doc.data();
            const li = document.createElement('li');
            li.textContent = `${msg.sender}: ${msg.message}`;
            chatMessages.appendChild(li);
          });
        });
    }
  </script>
</body>
</html>
