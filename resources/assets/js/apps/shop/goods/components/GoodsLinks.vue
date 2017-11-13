<template>
    <div class="row">
        <div class="links-filter-selector col-md-6">
        </div>
        <div class="col-md-6">

        </div>
    </div>
</template>
<script>
    let FilterSelector = require('../../../../components/filtersSelector.vue');

    module.exports = Vue.extend({
        props: {
            'id': Number,
            'entity': String,
        },
        data: function(){
            return {

            };
        },
        mounted() {
            new FilterSelector({
                el: this.setTarget(this.$el.getElementsByClassName('links-filter-selector')),
                'entity': 'Goods',
                'callback': function(){

                }
            });
        },
        methods: {

        }
    });
</script>
