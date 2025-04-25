function showSignup() {
  document.getElementById('loginForm').classList.remove('active');
  document.getElementById('signupForm').classList.add('active');
}

function showLogin() {
  document.getElementById('signupForm').classList.remove('active');
  document.getElementById('loginForm').classList.add('active');
}