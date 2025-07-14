const modal = document.getElementById("authModal");
const closeBtn = document.getElementById("closeModalBtn");
const formTitle = document.getElementById("formTitle");
const toggleText = document.getElementById("toggleForm");
const authForm = document.getElementById("authForm");


function openModal(type) {
  modal.style.display = "flex";

  const nameInput = authForm.querySelector("input[name='name']");
  if (type === 'login') {
    formTitle.innerText = "Log in to your account";
    authForm.action = "login.php";
    nameInput.style.display = "none";
    toggleText.innerHTML = `Don’t have an account? <a href="#" onclick="openModal('signup')">Sign up</a>`;
  } else {
    formTitle.innerText = "Create your account";
    authForm.action = "signup.php";
    nameInput.style.display = "block";
    toggleText.innerHTML = `Already have an account? <a href="#" onclick="openModal('login')">Log in</a>`;
  }
}
closeBtn.onclick = () => modal.style.display = "none";
window.onclick = (e) => {
  if (e.target === modal) modal.style.display = "none";
};

