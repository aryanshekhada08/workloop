
// ======== AUTH MODAL ========
const authModal = document.getElementById("authModal");
const authForm = document.getElementById("authForm");

// Open auth modal
document.getElementById("loginBtn").addEventListener("click", () => openAuthModal("login"));
document.getElementById("joinBtn").addEventListener("click", () => openAuthModal("signup"));

function openAuthModal(type) {
  authModal.classList.remove("hidden");
  toggleAuthType(type);
}

// Close auth modal
document.querySelectorAll(".closeModal").forEach(btn =>
  btn.addEventListener("click", () => authModal.classList.add("hidden"))
);

// Toggle login/signup UI
function toggleAuthType(type) {
  document.querySelector('[name="auth_type"]').value = type;

  const nameField = document.getElementById("nameField");
  const title = document.getElementById("authTitle");
  const submitBtn = document.getElementById("submitBtn");
  const nameInput = document.querySelector('[name="name"]');

  if (type === "signup") {
    nameField.classList.remove("hidden");
    nameInput.setAttribute("required", "required");
    title.textContent = "Create an account";
    submitBtn.textContent = "Join";
  } else {
    nameField.classList.add("hidden");
    nameInput.removeAttribute("required");
    title.textContent = "Welcome Back";
    submitBtn.textContent = "Login";
  }
}

// Handle auth form submission
authForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(authForm);

  try {
    const res = await fetch("auth.php", {
      method: "POST",
      body: formData,
    });

    const data = await res.json();

    if (data.success) {
      if (data.showRoleModal) {
        authModal.classList.add("hidden");
        document.getElementById("roleModal").classList.remove("hidden");
        document.getElementById("roleUserId").value = data.userId;
      } else if (data.redirect) {
        window.location.href = data.redirect;
      }
    } else {
      alert(data.message || "Invalid login/signup.");
    }
  } catch (err) {
    console.error(err);
    alert("Something went wrong.");
  }
});


document.addEventListener('DOMContentLoaded', function () {
    const freelancerBtn = document.getElementById('chooseFreelancer');
    const clientBtn = document.getElementById('chooseClient');

    if (freelancerBtn && clientBtn) {
        freelancerBtn.addEventListener('click', function () {
            selectRole('freelancer');
        });

        clientBtn.addEventListener('click', function () {
            selectRole('client');
        });
    }

    function selectRole(role) {
        fetch('set_role.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'role=' + encodeURIComponent(role)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert(data.message || 'Something went wrong.');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Failed to connect to server.');
        });
    }
});


