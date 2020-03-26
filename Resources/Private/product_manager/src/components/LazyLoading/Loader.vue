<template>
    <div class="overlay">
        <div class="lds-dual-ring"></div>
    </div>
</template>

<script>
    export default {
        name: "Loader"
    }
</script>

<style scoped>
    .overlay {
        background-color: rgba(255, 255, 255, 0.6);
        width: 100%;
        height: 100%;
        position: absolute;
        z-index: 2;
        top: 0;
    }
    .lds-dual-ring {
        display: inline-block;
        width: 80px;
        height: 80px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .lds-dual-ring:after {
        content: " ";
        display: block;
        width: 64px;
        height: 64px;
        margin: 8px;
        border-radius: 50%;
        border: 6px solid #cef;
        border-color: #cef transparent #cef transparent;
        animation: lds-dual-ring 1.2s linear infinite;
    }
    @keyframes lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

</style>