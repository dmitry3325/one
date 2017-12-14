<template>
    <div>
        <h1>{{ item.h1_title }}</h1>
        <button class="btn btn-success my-1" @click="save()">Сохранить</button>
        <editor v-model="content" @init="editorInit();" lang="html" theme="monokai" height="100"></editor>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                content: ""
            }
        },
        props: ['item'],
        components: {
            editor: require('vue2-ace-editor'),
        },
        methods: {
            editorInit: function () {
                require('brace/mode/html');
                require('brace/mode/javascript');
                require('brace/mode/less');
                require('brace/theme/monokai');
            },
            save: function () {
                Data.htmlPages.saveHtml(this.item.id, this.content);
            }
        },
        mounted: function () {
            let self = this;
            self.content = 'Загружаю...';

            Data.htmlPages.getHtmlMeta(self.item.id).then(
                data => {
                    self.content = '';
                    if(data.result && data.data.length > 0){
                        let body = data.data.find(meta => meta.key === 'body');
                        self.content = body.value;
                    }
                }
            );

        }
    }
</script>

<style lang="scss">

</style>