<template>
    <a-layout style="min-height: 100vh">
      
        <a-layout>
            <Header />
            <a-layout-content style="padding: 24px">
                <NuxtPage />
            </a-layout-content>
            <Footer />
        </a-layout>
    </a-layout>
</template>

<script>
import Header from '../components/Header/index.vue'
import Sidebar from '../components/Sidebar/index.vue'
import Footer from '../components/Footer/index.vue'
export default {
    components: {
        Header,
        Sidebar,
        Footer
    },
}
</script>