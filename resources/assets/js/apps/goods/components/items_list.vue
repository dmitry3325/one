<template>
    <div>
        <div v-for="item in items">{{item}}</div>
        <div>{{my_var}}</div>
    </div>
</template>
<script>
    module.exports = Vue.extend({
        data: function () {
            return {
                items: ['aa', 'bb'],
            }
        }
    });
</script>