<template>
    <div>
        <div class="tool-bar">
            <ol class="breadcrumb d-flex">
                <li v-if="selected" class="breadcrumb-item"><a target="_blank"
                                                               :href="selected.url">{{selected.title}}</a></li>
                <div class="ml-3 ml-auto">
                    <button type="button" class="btn btn-info">Добавить фильтр</button>
                </div>
                <div class="ml-3">
                    <button type="button" class="btn btn-warning">Настроить поля</button>
                </div>
                <div class="ml-3">
                    <button type="button" class="btn btn-success" @click="createEntity($event)">Создать</button>
                </div>
            </ol>
        </div>
        <div class="list">
            <div v-if="loading" class="text-center">
                <h5>Подождите, данные загружаются...</h5>
            </div>
            <div v-else>
                <table v-if="Object.keys(items).length > 0" class="table table-bordered table-striped">
                    <tr>
                        <th v-for="field in fields">{{field.title}}</th>
                    </tr>
                    <tr v-for="item in items">
                        <th v-for="(field,index) in fields">{{item[index]}}</th>
                    </tr>
                </table>
                <div class="text-center"><h3>Ничего не нашлось</h3></div>
            </div>
        </div>
    </div>
</template>
<script>
    module.exports = Vue.extend({
        data: function () {
            return {
                'entity': null,
                'loading': true,
                'items': {},
                'selected': {},
                'filters': {},
                'fields': {},
            }
        },
        mounted: function () {
            console.log(this)
            this.loadData();
        },
        methods: {
            loadData(){
                let self = this;
                Ajax.post('/shop/lists', 'getItemsList', {
                    entity: this.entity,
                    filters: this.filters,
                    fields: this.fields
                }, function (res) {
                    self.loading = false;

                    console.log(res)
                });
            },
            createEntity($event){
                let entity = this.entity;
                $event.target.classList.add('disabled');
                Ajax.post('/shop/lists', 'createEntity', {
                    entity: entity
                }, function (res) {
                    if (res.result && res.id) {
                        window.open('/shop/item?type=' + entity + '&id=' + res.id, '_blank');
                    }
                    $event.target.classList.remove('disabled');
                });
            }
        }
    });
</script>