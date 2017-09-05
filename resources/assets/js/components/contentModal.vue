<template>
    <div class="modal content-modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <span  class="close glyphicon glyphicon-remove" data-dismiss="modal" aria-label="Close"></span>
                <div v-if="header" class="modal-header">
                    <h4>{{ header }}</h4>
                </div>
                <div class="modal-body" v-html="((body) ? body : '')">
                </div>
                <div v-if="footer" class="modal-footer">
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    module.exports = Vue.extend({
        props: {
            'header': {
                'default': null,
            },
            'body': {
                'default': null,
            },
            'footer': {
                'default': null,
            }
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
            getBody(){
               return this.$el.querySelector('.modal-body');
            }
        }
    });
</script>
<style>
    .modal h4 {
        margin-bottom: 0px;
    }
    .content-modal .modal-dialog{
        margin: 50px auto;
    }
    .content-modal .glyphicon{
        position: absolute;
        right: -30px;
        top: -30px;
        color:white;
    }

</style>