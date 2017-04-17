<template>
    <div class="col-xs-12 text-center">
        <strong>{{ this.getResults().currentPage }} of {{ this.getResults().lastPage }}</strong><br>
        <a v-if="shouldShowPrev()" @click="prev($event)" :href="this.getResults().prevPageUrl">Prev</a>
        <a v-if="shouldShowNext()" @click="next($event)" :href="this.getResults().nextPageUrl">Next</a>
    </div>
</template>

<style lang="scss">
    @import '../../sass/common.scss';
</style>

<script>
    export default {
        props: [],
        mounted() {
            window.bookMe.Event.listen('validation-error', (e) => this.handleError(e));
        },
        methods: {
            getResults() {
                return window.bookMe.store.searchResults;
            },
            prev(e) {
                e.preventDefault();
                window.bookMe.Event.fire('search-prev');
            },
            next(e) {
                e.preventDefault();
                window.bookMe.Event.fire('search-next');
            },
            handleError(error) {
                //TODO - Implement error handling/display/etc
                //TODO - this.errors = {}
            },
            shouldShowNext() {
                return (null !== this.getResults().nextPageUrl);
            },
            shouldShowPrev() {
                return (null !== this.getResults().prevPageUrl);
            },
        },
        data() {
            return {
                errors: {}
            }
        }
    }
</script>