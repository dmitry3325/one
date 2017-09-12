<template>
    <div class="filters">
        <div v-if="loading">Подождите, данные загружаются...</div>
        <div v-else>
            <div v-if="entity == 'Sections'">
                <div v-for="filter in filters" class="row">
                    <div class="col-md-2">
                        <span class="badge badge-default">Фильтр №{{filter.num}}</span>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" v-model="filter.value" placeholder="Введите значение фильтра">
                    </div>
                    <div class="col-md-3">
                    </div>
                    <div class="col-md-12"><hr/></div>
                </div>
            </div>
            <div v-else>

            </div>
        </div>

    </div>
</template>
<script>
    module.exports = Vue.extend({
        props: {
            entity: String,
            id: Number
        },
        data: function () {
            return {
                filters: [],
                Section: {},
                Model: {},
                loading: true
            }
        },
        mounted: function () {
            this.getData();
            this.addNewFilter();
        },
        methods: {
            getData() {
                let self = this;
                Data.entity.get(this.entity, this.id).then(function (model) {
                    self.Model = model;
                    if (model.section_id) {
                        Data.sections.get(model.section_id).then(function (section) {
                            self.Section = section;
                            self.loading = false;
                        });
                    } else {
                        self.loading = false;
                    }
                });
            },
            addNewFilter() {
                this.filters.push({
                    num: this.filters.length + 1,
                    value: ''
                });
            },
            addFilter(value, num) {
                Ajax.post('/shop', 'addFilter', {
                    'entity': this.entity,
                    'id': this.id,
                    'value': value,
                    'num': num
                }).then(function () {

                });
            }
        }
    });
</script>
<style>
    .filters .badge{
        font-size: 18px;
        width:100%;
        padding: 10px 15px;
    }
</style>