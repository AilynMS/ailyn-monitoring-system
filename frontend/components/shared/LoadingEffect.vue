<template>
    <div class="load-wrapp">
        <div 
            :class="size !== 'sm' ? 'md' : 'sm'" 
            class="load">
            
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </div>  
</template>

<script>
export default {
    name: 'LoadingEffect',

    props: {
        size: {
            required: false,
            default: 'sm'
        }
    }
}
</script>

<style lang="scss" scoped>
    .load-wrapp {
        text-align: center;

        .load .line:nth-last-child(1) {
            animation: loadingC 0.6s 0.1s linear infinite;
        }
        .load .line:nth-last-child(2) {
            animation: loadingC 0.6s 0.2s linear infinite;
        }
        .load .line:nth-last-child(3) {
            animation: loadingC 0.6s 0.3s linear infinite;
        }

        .line {
            display: inline-block;
            border-radius: 50%;
            background-color: #FF6C00;
        }

        .load.sm {
            .line {
                width: 10px;
                height: 10px;
            }
        }

        .load.md {
            .line {
                width: 16px;
                height: 16px;
            }
        }
    }

    @keyframes loadingC {
        0% {
            transform: translate(0, 0);
        }
        50% {
            transform: translate(0, 10px);
        }
        100% {
            transform: translate(0, 0);
        }
    }
</style>