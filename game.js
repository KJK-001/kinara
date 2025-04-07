// Firebase Configuration
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

// DOM Elements
const signInBtn = document.getElementById('sign-in-btn');
const signOutBtn = document.getElementById('sign-out-btn');
const transferPlayerBtn = document.getElementById('transfer-player-btn');
const challengesList = document.getElementById('challenges-list');
const joinGameBtn = document.getElementById('join-game-btn');
const startGameBtn = document.getElementById('start-game-btn');
const chatInput = document.getElementById('chat-input');
const sendChatBtn = document.getElementById('send-chat-btn');
const createTeamBtn = document.getElementById('create-team-btn');
const teamNameInput = document.getElementById('team-name');
const userTeamsList = document.getElementById('user-teams');

// Handle Sign In and Sign Out
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
  }
}

// Load User Data
function loadUserData() {
  const userId = auth.currentUser.uid;

  firestore.collection('Users').doc(userId).get().then(doc => {
    const userData = doc.data();
    document.getElementById('user-name').textContent = `Welcome, ${userData.name}`;
    document.getElementById('user-coins').textContent = userData.totalCoins;
    document.getElementById('user-diamonds').textContent = userData.totalDiamonds;
  });
}

// Transfer Player Coins
transferPlayerBtn.addEventListener('click', () => {
  const userId = auth.currentUser.uid;

  firestore.collection('Users').doc(userId).update({
    totalCoins: firebase.firestore.FieldValue.increment(-100),
  }).then(() => {
    alert('Player transfer successful!');
  }).catch(() => {
    alert('Not enough coins!');
  });
});

// Fetch Daily Challenges
function loadDailyChallenges() {
  firestore.collection('DailyChallenges').get().then(snapshot => {
    snapshot.docs.forEach(doc => {
      const challenge = doc.data();
      const li = document.createElement('li');
      li.textContent = `${challenge.name} - Reward: ${challenge.rewardCoins} Coins`;
      const button = document.createElement('button');
      button.textContent = 'Complete Challenge';
      button.onclick = () => completeChallenge(doc.id, challenge.rewardCoins);
      li.appendChild(button);
      challengesList.appendChild(li);
    });
  });
}

// Complete Daily Challenge
function completeChallenge(challengeId, rewardCoins) {
  const userId = auth.currentUser.uid;

  firestore.collection('Users').doc(userId).update({
    totalCoins: firebase.firestore.FieldValue.increment(rewardCoins),
  }).then(() => {
    firestore.collection('DailyChallenges').doc(challengeId).update({
      isCompleted: true,
    });
    alert(`You earned ${rewardCoins} coins!`);
  });
}

// Multiplayer Game Logic
let gameRoomId = null;

joinGameBtn.addEventListener('click', () => {
  const userId = auth.currentUser.uid;
  firestore.collection('Games').add({
    player: userId,
    opponent: null,
    status: 'waiting',
  }).then(gameRoom => {
    gameRoomId = gameRoom.id;
    listenForOpponent(gameRoom.id);
  });
});

function listenForOpponent(roomId) {
  firestore.collection('Games').doc(roomId).onSnapshot(doc => {
    const data = doc.data();
    if (data.opponent) {
      document.getElementById('game-msg').textContent = `Game started with ${data.opponent}`;
      startGameBtn.style.display = 'inline-block';
      document.getElementById('waiting-msg').style.display = 'none';
    }
  });
}

startGameBtn.addEventListener('click', () => {
  alert('Game started!');
});

// Chat Functionality
sendChatBtn.addEventListener('click', () => {
  const message = chatInput.value;
  if (message) {
    const userId = auth.currentUser.uid;
    firestore.collection('Games').doc(gameRoomId).collection('chat').add({
      sender: userId,
      message: message,
      timestamp: firebase.firestore.FieldValue.serverTimestamp(),
    });
    chatInput.value = '';
  }
});

// Listen to Chat Messages
function loadChatMessages() {
  firestore.collection('Games').doc(gameRoomId).collection('chat')
    .orderBy('timestamp')
    .onSnapshot(snapshot => {
      snapshot.docs.forEach(doc => {
        const msg = doc.data();
        const li = document.createElement('li');
        li.textContent = `${msg.sender}: ${msg.message}`;
        document.getElementById('chat-messages').appendChild(li);
      });
    });
}

// Team Management
createTeamBtn.addEventListener('click', () => {
  const teamName = teamNameInput.value;
  const userId = auth.currentUser.uid;

  firestore.collection('Teams').add({
    name: teamName,
    members: [userId],
    wins: 0,
    losses: 0,
  }).then(() => {
    alert('Team created!');
    teamNameInput.value = '';
    loadUserTeams();
  });
});

function loadUserTeams() {
  const userId = auth.currentUser.uid;

  firestore.collection('Teams').where("members", "array-contains", userId)
    .get()
    .then(snapshot => {
      snapshot.docs.forEach(doc => {
        const team = doc.data();
        const li = document.createElement('li');
        li.textContent = `${team.name}`;
        userTeamsList.appendChild(li);
      });
    });
}
