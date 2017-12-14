<template>
    <div>
        <h1>{{ item.h1_title }}</h1>
        <input v-model="url" class="input-white" placeholder="url">
        <button class="btn btn-success my-1" @click="save()">Сохранить</button>

        <editor v-model="content" @init="editorInit();" lang="html" theme="monokai" height="500px"></editor>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                content: "",
                url: null,
            }
        },
        props: ['item'],
        computed: {
            page: {
                get: function () {
                    return {
                        body: this.content,
                        url: this.url
                    }
                }
            }
        },
        components: {
            editor: require('vue2-ace-editor'),
        },
        methods: {
            editorInit: function () {
                require('brace/mode/html');
                require('brace/theme/monokai');
            },
            save: function () {

                if (!this.url) {
                    AppNotifications.add({
                        'type': 'danger',
                        'body': 'Заполните url'
                    });

                    return;
                }

                Data.htmlPages.update(this.item.id, this.page).then(_ => {
                    AppNotifications.add({
                        'type': 'success',
                        'body': 'сохранено'
                    });
                }).catch(e => {
                    AppNotifications.add({
                        'type': 'danger',
                        'body': 'Ошибка - ' + e
                    });
                });
            }
        },
        mounted: function () {
            let self = this;
            self.content = 'Загружаю...';
            self.url = self.item.url.url;

            Data.htmlPages.getHtmlMeta(self.item.id).then(
                data => {
                    self.content = '';
                    if (data.result && data.data.length > 0) {
                        let body = data.data.find(meta => meta.key === 'body');
                        self.content = body.value;
                    }
                }
            );

        }
    }
</script>

<style lang="scss">
    .input-white {
        width: 200px;
        display: block;
        background: #ccc;
        border: 1px solid #8c8c8c;
        border-radius: 2px;
    }
</style>