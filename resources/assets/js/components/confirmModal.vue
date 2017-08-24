<template>
    <div class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>{{(header) ? header : 'Подтвердите!'}}</h4>
                </div>
                <div class="modal-body">
                    {{(body) ? body : 'Вы уверены?'}}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" @click="cancel">Нет</button>
                    <button class="btn btn-primary" @click="confirm">Да</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    module.exports = Vue.extend({
        data: function () {
            return {
                'header': null,
                'body': null,
                'confirm_func': null,
                'cancel_func': null
            };
        },
        beforeCreate: function () {
            this.$options.el = this.setTarget('body');
        },
        mounted() {
            let self = this;
            $(self.$el).modal('show');
            $(self.$el).on('hidden.bs.modal', function (e) {
                self.$el.remove();
                self.$destroy();
            });
        },
        methods: {
            confirm: function(){
                if(typeof this.confirm_func === 'function'){
                    this.confirm_func();
                }
                $(this.$el).modal('hide');

            },
            cancel : function(){
                if(typeof this.cancel_func === 'function'){
                    this.cancel_func();
                }
                $(this.$el).modal('hide');
            }
        }
    });
</script>
<style>
    .modal h4 {
        margin-bottom: 0px;
    }
</style>