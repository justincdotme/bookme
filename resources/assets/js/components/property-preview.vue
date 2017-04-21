<template>
    <div class="col-xs-4">
        <div class="property-preview">
            <h1>{{ property.name }}</h1>
            <div class="row">
                <div v-for="image in this.images">
                    <div class="col-xs-4">
                        <a class="thumbnail">
                            <img :src="image.thumb_path">
                        </a>
                    </div>
                </div>
            </div>
            <p>{{ property.long_description }}</p>
        </div>
    </div>
</template>

<style lang="scss">
    @import '../../sass/common.scss';
    .property-preview {
        border-radius: 0;
        border: 1px solid $med-blue;
        margin: 1em auto;
        height: 250px;
    }
</style>

<script>
    export default {
        props: [
            'property',
            'images'
        ],
        mounted() {
            this.images = this.property.images;
        },
        methods: {},
        data() {
            return {}
        }
    }
</script>