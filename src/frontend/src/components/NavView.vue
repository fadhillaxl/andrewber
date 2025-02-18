<template>
    <button class="absolute top-0 right-0 m-4" @click="$router.push('/logout')">Logout</button>
    <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-md">
      <ul class="flex justify-around">
        <li><a href="/home" class="p-4">Home</a></li>
        <li><a href="#about" class="p-4">About</a></li>
        <li><a href="/profile" class="p-4">Profile</a></li>
      </ul>
    </nav>
</template>