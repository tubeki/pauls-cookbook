<script setup>
// No logic yet
</script>

<template>
  <div class="container">
    <main>
      <form class="login-form">
        <h2>Login</h2>
        <div>
          <label for="email">Email</label>
          <input type="email" id="email" placeholder="Enter your email" required />
        </div>
        <div>
          <label for="password">Password</label>
          <input type="password" id="password" placeholder="Enter your password" required />
        </div>
        <button type="submit">Log In</button>
        <p class="footer-text">
          Don’t have an account? <a href="#">Sign up</a>
        </p>
      </form>
    </main>
  </div>
</template>

<style scoped>
.container{max-width:960px;margin:0 auto}
main{padding:2rem;display:flex;justify-content:center}
.login-form{background:#fff;padding:2rem;border:1px solid #ddd;border-radius:12px;box-shadow:0 1px 2px rgba(0,0,0,.05);width:100%;max-width:400px}
.login-form h2{margin-top:0;color:#222}
label{display:block;margin-bottom:.5rem;font-weight:bold}
input{width:100%;padding:.6rem;border:1px solid #ccc;border-radius:8px;margin-bottom:1rem;font-size:1rem}
button{background:#007bff;color:#fff;border:none;padding:.7rem 1.2rem;border-radius:10px;cursor:pointer;font-size:1rem;width:100%}
button:hover{background:#0056b3}
.footer-text{text-align:center;margin-top:1rem;font-size:.9rem;color:#555}
.footer-text a{color:#007bff;text-decoration:none}
.footer-text a:hover{text-decoration:underline}
</style>
