<!-- auth-modal.php -->
<div id="authModal" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50 hidden p-4">
  <div class="relative flex flex-col md:flex-row w-full max-w-4xl bg-white rounded-2xl overflow-hidden shadow-2xl">

    <!-- CLOSE BUTTON -->
    <button onclick="closeModal()" class="absolute top-2 right-3 text-gray-500 hover:text-black text-2xl font-bold z-10">
      &times;
    </button>

    <!-- LEFT PANEL -->
    <div class="w-full md:w-1/2 bg-[#912c2c] text-white p-6 flex flex-col justify-center items-center text-center">
      <h2 class="text-2xl md:text-3xl font-bold mb-4">Success starts here</h2>
      <ul class="space-y-2 text-sm md:text-base">
        <li>✔ Over 700 categories</li>
        <li>✔ Quality work done faster</li>
        <li>✔ Access to talent and businesses across the globe</li>
      </ul>
      <div class="mt-6 w-3/4 max-w-[250px]">
        <img src="assets/image/auth-bg.png" alt="Work" class="w-full mx-auto rounded">
      </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="w-full md:w-1/2 p-6 md:p-8 bg-white">
      <h2 id="modalTitle" class="text-xl md:text-2xl font-semibold mb-4 text-center">Create a new account</h2>
     <!-- Error Message -->
      <p id="authError" class="text-red-500 text-sm hidden"></p>
      <div id="formError" class="text-red-500 mt-2"></div>
      <form id="authForm" class="space-y-4">
        <input type="hidden" name="auth_type" id="auth_type" value="signup">

        <!-- Name Field (Signup only) -->
       <div id="nameField" class="hidden">
            <label for="name" class="block text-sm font-medium">Name</label>
            <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded px-3 py-2" />       
        </div>
        <!-- Email Field -->
        <div>
          <label class="block mb-1 text-gray-700">Email</label>
          <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded px-3 py-2" >
        </div>

        <!-- Password Field -->
        <div>
          <label class="block mb-1 text-gray-700">Password</label>
          <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded px-3 py-2" >
        </div>

        <!-- Submit Button -->
        <button type="submit" class="bg-[#1DBF73] text-white px-4 py-2 rounded w-full hover:bg-[#19a563] transition">
          Continue
        </button>
        <!-- Footer Links -->
        <div class="auth-footer text-center text-sm mt-4">
          <!-- Injected by JS -->
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Role Selection Modal -->
<div id="roleModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
  <div class="bg-white rounded-lg p-6 shadow-md w-full max-w-sm text-center">
    <h3 class="text-xl font-semibold mb-4 justify-center items-center">Select Your Role</h3>
    <div class="flex flex-col md:flex-row gap-4">
      <button id="chooseFreelancer" class="bg-blue-500 text-white px-4 py-2 rounded">Freelancer</button>
      <button id="chooseClient" class="bg-green-500 text-white px-4 py-2 rounded">Client</button>
    </div>
  </div>
</div>
