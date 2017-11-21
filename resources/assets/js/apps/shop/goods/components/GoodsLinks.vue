<template>
    <div class="row" v-show="dataLoaded">
        <div class="links-filter-selector col-md-6">
            <h1>Укажите подходящие товары:</h1>
            <filter-selector
                    ref="filter_selector"
                    :entity="entity"
                    :filters="Model.filters"
                    :no_save_button="true"
                    :callback="filterSelectorCallback">
                :ls_storage_key="null"
            </filter-selector>
        </div>
        <div class="col-md-6">
            <div style="margin-top:30px;" class="row">
                <div class="col-md-6">
                    <div class="label">Максимальное кол-во товаров:</div>
                    <input type="number" class="form-control" v-model="Model.limit">
                </div>
                <div class="col-md-6">
                    <div class="label">Сортировка по умолчанию:</div>
                    <div class="dropdown">
                        <button type="button" class="btn  btn-secondary dropdown-toggle" data-toggle="dropdown">
                            {{ ((Model.sort) ? Sorting[Model.sort] : 'Укажите сортировку') }}
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#"
                               v-for="(option,index) in Sorting"
                               :class="(Model.sort && Model.sort === index)?'active':''"
                               @click="$set(Model, 'sort', index)">
                                {{option}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    let FilterSelector = require('../../../../components/filtersSelector.vue');

    module.exports = Vue.extend({
        props:      {
            'id':         Number,
            'entity':     String,
            'links_data': String
        },
        components: {
            'filter-selector': FilterSelector
        },
        data:       function () {
            return {
                'dataLoaded': false,
                'Model':      {},
                'Sorting':    {
                    'asc':    'По возрастанию',
                    'desc':   'По убываниию',
                    'random': 'В случайном порядке',
                },
            };
        },
        mounted() {
            let self = this;

            if (typeof this.links_data !== 'undefined') {
                self.$set(self, 'Model', JSON.parse(this.links_data));
                self.$set(self, 'dataLoaded', true);
            } else {
                Data.entity.get(this.entity, this.id, true)
                    .then(function (res) {
                        let data = res.goods_links_data;
                        if (!data) {
                            data = {};
                        } else {
                            data = JSON.parse(data);
                        }
                        self.$set(self, 'Model', data);
                        self.$set(self, 'dataLoaded', true);
                    });
            }
        },
        methods:    {
            getFilterSelectorData
            getData() {
                return this.Model;
            }
        }
    });
</script>
