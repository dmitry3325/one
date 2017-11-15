<template>
    <div class="row" v-show="dataLoaded">
        <div class="links-filter-selector col-md-6">
            <h1>Укажите подходящие товары:</h1>
            <filter-selector
                    :entity="entity"
                    :filters="Model.filters"
                    :no_save_button="true"
                    :callback="filterSelectorCallback">
                :ls_storage_key="null"
            </filter-selector>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="label">Максимальное кол-во товаров:</div>
                    <input type="number" class="form-control" v-model="Model.limit">
                </div>
                <div class="col-md-6">
                    <div class="label">Сортировка по умолчанию:</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    let FilterSelector = require('../../../../components/filtersSelector.vue');

    module.exports = Vue.extend({
        props:      {
            'id':     Number,
            'entity': String,
        },
        components: {
            'filter-selector': FilterSelector
        },
        data:       function () {
            return {
                'dataLoaded': false,
                'Model':      {},
            };
        },
        mounted() {
            let self = this;
            Data.entity.get(this.entity, this.id, true)
                .then(function (res) {
                    let data = res.goods_links_data;
                    if (!data) {
                        data = {};
                    }
                    self.$set(self, 'Model', data);
                    self.$set(self, 'dataLoaded', true);
                });
        },
        methods:    {
            filterSelectorCallback() {

            }
        }
    });
</script>
