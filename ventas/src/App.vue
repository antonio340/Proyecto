<template>
  <Header v-if="$route.path !== '/'"/>
  <router-view/>
</template>

<script>
import Header from './components/shared/Header.vue'

export default {
  name: 'App',
  components: {
    Header
  }
}
</script>

<style>
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
