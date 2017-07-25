<template>
    <div class="path">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Library</a></li>
            <li class="breadcrumb-item active">Data</li>
        </ol>
    </div>
</template>
<script>
    module.exports = Vue.extend({
        mounted: function () {
            console.log(this.stage)
        }
    });
</script>