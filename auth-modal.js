// Elements
const authModal = document.getElementById("authModal");
const authForm = document.getElementById("authForm");
const nameField = document.getElementById("nameField");
const authTypeInput = document.getElementById("auth_type");
const modalTitle = document.getElementById("modalTitle");
const authFooter = document.querySelector(".auth-footer");
const authError = document.getElementById("authError");
const formError = document.getElementById("formError");
const roleModal = document.getElementById("roleModal");

// OPEN modal
function openAuthModal(type = "signup") {
  authModal.classList.remove("hidden");
  setAuthType(type);
}

// CLOSE modal
function closeModal() {
  if (authModal) authModal.classList.add("hidden");
  if (roleModal) roleModal.classList.add("hidden");
}

// Toggle auth type between login/signup
function setAuthType(type) {
  authTypeInput.value = type;

  if (type === "signup") {
    nameField.classList.remove("hidden");
    modalTitle.textContent = "Create a new account";
    authFooter.innerHTML = `Already have an account? <button onclick="setAuthType('login')" class="text-[#1DBF73] underline">Login</button>`;
  } else {
    nameField.classList.add("hidden");
    modalTitle.textContent = "Welcome back";
    authFooter.innerHTML = `Don't have an account? <button onclick="setAuthType('signup')" class="text-[#1DBF73] underline">Sign up</button>`;
  }

  authError.classList.add("hidden");
  formError.textContent = "";
  // Ensure roleModal is hidden when switching auth type
  if (roleModal) roleModal.classList.add("hidden");
}

// Form submit handler
authForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  formError.textContent = "";

  const formData = new FormData(authForm);
  const submitBtn = authForm.querySelector("button[type='submit']");
  submitBtn.disabled = true;
  submitBtn.textContent = "Processing...";

  try {
    const res = await fetch("auth.php", {
      method: "POST",
      body: formData,
    });

    const data = await res.json();
    submitBtn.disabled = false;
    submitBtn.textContent = "Continue";

    if (data.success) {
      if (data.redirect) {
        window.location.href = data.redirect;
      } else if (data.show_role_modal) {
        authModal.classList.add("hidden");
        roleModal.classList.remove("hidden");
      }
    } else {
      formError.textContent = data.message || "Authentication failed.";
    }
  } catch (err) {
    console.error("Auth error:", err);
    formError.textContent = "Something went wrong. Please try again.";
    submitBtn.disabled = false;
    submitBtn.textContent = "Continue";
  }
});

// Role selection buttons
document.getElementById("chooseFreelancer").addEventListener("click", () => {
  selectRole("freelancer");
});

document.getElementById("chooseClient").addEventListener("click", () => {
  selectRole("client");
});

function selectRole(role) {
  fetch("set_role.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `role=${encodeURIComponent(role)}`,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success && data.redirect) {
        window.location.href = data.redirect;
      } else {
        alert(data.message || "Failed to set role.");
      }
    })
    .catch((err) => {
      console.error("Role error:", err);
      alert("Could not connect to server.");
    });
}

 window.openAuthModal = openAuthModal;
// Close button listener
const closeAuthBtn = document.getElementById("closeAuthBtn");
if (closeAuthBtn) {
  closeAuthBtn.addEventListener("click", closeModal);
}

// Optional: Close modal when clicking outside the modal content
authModal?.addEventListener("click", function (e) {
  if (e.target === authModal) {
    closeModal();
  }
});
