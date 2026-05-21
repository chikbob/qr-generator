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
    display: flex;
    flex-direction: column;
}

.layout-content {
    flex: 1;
    width: 100%;
    padding: 20px;
}
</style>
