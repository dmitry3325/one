<template>
    <div class="row" v-show="dataLoaded">
        <div class="links-filter-selector col-md-6">
            <h1>Укажите подходящие товары:</h1>
            <filter-selector
                    :entity="entity"
                    :callback="filterSelectorCallback">
                    :ls_storage_key="null"
            </filter-selector>
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
        components: {
            'filter-selector': FilterSelector
        },
        data: function(){
            return {
                'dataLoaded' : false,
            };
        },
        mounted() {
            Data.entity.get(this.entity, this.id, true).then(function (res) {

            });
        },
        methods: {
            filterSelectorCallback(){

            }
        }
    });
</script>
