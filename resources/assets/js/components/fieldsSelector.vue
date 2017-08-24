<template>
    <div id="fieldsSelector" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Выберите поля</h4>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <draggable v-model="fields" @start="startMove" @end="endMove">
                            <transition-group>
                                <li v-for="(field,index) in fields" :key="index"
                                    class="list-group-item justify-content-between">
                                    <span>{{((all_fields[field])?all_fields[field].title:field) }}</span>
                                    <span @click.prevent="remove(index)"
                                          class="float-right glyphicon glyphicon-remove"></span>
                                </li>
                            </transition-group>
                        </draggable>
                    </div>
                    <div class="btn-group">
                        <input type="text" placeholder="Добавьте поля" v-model="search"
                               class="form-control dropdown-toggle" data-toggle="dropdown">
                        <div class="dropdown-menu">
                            <a v-for="(field, index) in all_fields"
                               @click="fields.push(index)"
                               v-show="((!search || field.title.indexOf(search)!==-1)?true:false)"
                               class="dropdown-item" href="#">{{field.title}}</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="setBaseFields" class="mr-auto btn btn-secondary">Поля по умолчанию</button>
                    <button class="btn btn-primary" @click="finish">Готово</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    module.exports = Vue.extend({
        components: {
            'draggable': require('vuedraggable'),
        },
        data: function () {
            return {
                'entity': null,
                'fields': [],
                'all_fields': {},
                'base_fields': [],
                'search': '',
                'callback': function (fields) {
                    console.log('Выбраны поля: ', fields);
                }
            };
        },
        mounted() {
            if (!this.entity) {
                return false;
            }

            this.loadData();

            let self = this;
            $(this.$el).modal('show');
            $(this.$el).on('hidden.bs.modal', function (e) {
                self.$el.remove();
                self.$destroy();
            });
        },
        methods: {
            loadData() {
                let self = this;

                return Ajax.when([
                    Data.entity.getAllFields(this.entity),
                    Data.entity.getBaseFields(this.entity)
                ], function (fields, base_fields) {
                    if(!self.fields.length) {
                        self.fields = base_fields.slice(0);
                    }
                    self.base_fields = base_fields.slice(0);
                    self.all_fields = fields;
                });
            },
            setBaseFields(){
                this.fields = this.base_fields.slice(0);
            },
            finish() {
                $(this.$el).modal('hide');
                if (typeof this.callback === 'function') {
                    this.callback(this.fields.slice(0));
                }
            },
            startMove(e) {
                e.item.classList.add('selected');
            },
            remove(index) {
                this.fields.splice(index, 1);
            },
            endMove(e) {
                [].map.call(e.target.querySelectorAll('.selected'), function (el) {
                    el.classList.remove('selected');
                });
            }
        }
    });
</script>

<style>
    #fieldsSelector h4 {
        margin-bottom: 0px;
    }

    #fieldsSelector .list-group-item {
        cursor: pointer;
    }

    #fieldsSelector .btn-group {
        width: 100%;
    }

    #fieldsSelector .title-button {
        width: calc(100% - 100px);
    }

    #fieldsSelector .list-group-item.selected {
        background: #eee;
    }
</style>