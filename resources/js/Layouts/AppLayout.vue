<template>
    <div class="layout-wrapper">
        <Header/>

        <main class="layout-content">
            <slot/>
        </main>

        <Footer/>
    </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
</script>

<style scoped>
.layout-wrapper {
    min-height: 100dvh;
    height: 100dvh;
}

.layout-content {
    padding: 20px;
}
</style>
