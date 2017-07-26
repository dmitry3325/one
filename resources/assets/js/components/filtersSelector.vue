<template>
    <div id="filtersCont" class="modal fade" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Выберите фильтры</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-for="filter in filters" class="alert alert-info">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="save()" class="btn btn-success">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    module.exports = Vue.extend({
        data: function () {
            return {
                'entity': null,
                'allFields': {},
                'filters': [],
                'callback': null,
            };
        },
        mounted() {
            if(!this.entity){
                return false;
            }

            Data.entity.getAllFields(this.entity).then(function(data){
                console.log(data)
            });

            $(this.$el).modal('show')
        },
        methods: {
            loadFieldsData(){
                console.log(this.entity)
            }
        }
    });
</script>
