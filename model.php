<!-- AUTH MODAL -->
<div id="authModal" class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50 hidden p-4">
  <div class="relative flex flex-col md:flex-row w-full max-w-4xl bg-white rounded-2xl overflow-hidden shadow-2xl">
    
    <!-- CLOSE BUTTON  -->
    <button id="closeAuthBtn" class="absolute top-2 right-3 text-gray-500 hover:text-black text-2xl font-bold z-10">
      &times;
    </button>

    <!-- LEFT PANEL -->
  <div class="w-full md:w-1/2 text-white p-6 flex flex-col justify-center items-center text-center bg-cover bg-center" style="background-image: url('assets/image/auth-bg.png');  background-blend-mode: darken;">
  <h2 class="text-2xl md:text-3xl font-bold mb-13">Success starts here</h2>
  <ul class="space-y-2 text-sm md:text-base  mb-10">
    <li>✔ Over 700 categories</li>
    <li>✔ Quality work done faster</li>
    <li>✔ Access to talent and businesses across the globe</li>
  </ul>
</div>



    <!-- RIGHT PANEL -->
    <div class="w-full md:w-1/2 p-6 md:p-8 bg-white">
      <h2 id="modalTitle" class="text-xl md:text-2xl font-semibold mb-4 text-center">Create a new account</h2>
      <p id="authError" class="text-red-500 text-sm hidden"></p>
      <div id="formError" class="text-red-500 mt-2"></div>
      <form id="authForm" class="space-y-4">
        <input type="hidden" name="auth_type" id="auth_type" value="signup" />

        <!-- Name Field -->
        <div id="nameField" class="hidden">
          <label for="name" class="block text-sm font-medium">Name</label>
          <input type="text" name="name" id="name" class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <!-- Email -->
        <div>
          <label class="block mb-1 text-gray-700">Email</label>
          <input type="email" name="email" id="email" class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <!-- Password -->
        <div>
          <label class="block mb-1 text-gray-700">Password</label>
          <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <!-- Submit -->
        <button type="submit" class="bg-[#1DBF73] text-white px-4 py-2 rounded w-full hover:bg-[#19a563] transition">
          Continue
        </button>

        <!-- Footer Links -->
        <div class="auth-footer text-center text-sm mt-4"></div>
      </form>
    </div>
  </div>
</div>

<!-- ROLE SELECTION MODAL -->
<div id="roleModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex justify-center items-center z-50 hidden p-4">
  <div class="bg-white rounded-2xl p-8 shadow-2xl w-full max-w-md text-center relative animate-fade-in">
    

    <h3 class="text-2xl font-bold text-gray-800 mb-6">Choose Your Role</h3>

    <div class="flex flex-col sm:flex-row gap-6 justify-center">
      <!-- Freelancer Button -->
      <button id="chooseFreelancer" class="w-full sm:w-1/2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-lg font-medium py-3 rounded-xl shadow-md hover:shadow-lg transition duration-300">
        I am Freelancer
      </button>

      <!-- Client Button -->
      <button id="chooseClient" class="w-full sm:w-1/2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-lg font-medium py-3 rounded-xl shadow-md hover:shadow-lg transition duration-300">
         I am Client
      </button>
    </div>
  </div>
</div>
