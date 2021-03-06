require('./bootstrap');

//Components
import PropertyPreview from './components/property-preview.vue';
import SearchPaginator from './components/search-paginator.vue';
import SearchWidget from './components/search-widget.vue';
import VeeValidate from 'vee-validate';
import { Validator } from 'vee-validate';

const dictionary = {
    custom : {
        state: {
            excluded:  () =>"Please select a state."
        }
    }
};
Validator.localize('en', dictionary);

window.Vue.use(VeeValidate);
window.bookMe.stateManager.init();

//Search Results
window.bookMe.searchResultsPage = new Vue({
    el: '#search-results',
    data: {
        store: window.bookMe.stateManager.getStore(),
        properties: window.bookMe.store.searchResults.data,
        results: window.bookMe.stateManager.getSearchResults()
    },
    components: {
        PropertyPreview,
        SearchPaginator,
        SearchWidget
    },
    mounted() {
        this.updateResults(window.results);
        window.bookMe.Event.listen('search-prev', () => this.paginatePrev());
        window.bookMe.Event.listen('search-next', () => this.paginateNext());
        window.bookMe.Event.listen('city-state-search', (query) => this.search(query));
    },
    methods: {
        updateList() {
            this.properties = this.getProperties();
            this.results = window.bookMe.stateManager.getSearchResults();
        },
        getProperties() {
            return window.bookMe.stateManager.getSearchResults().data;
        },
        updateResults(results) {
            window.bookMe.stateManager.updateSearchResults(results);
            this.updateList();
        },
        paginatePrev() {
            this.fetchProperties(this.results.prevPageUrl);
        },
        paginateNext() {
            this.fetchProperties(this.results.nextPageUrl);
        },
        search(query) {
            let queryUrl = '/properties/search?searchType=city-state&city=';
            queryUrl += query.city;
            queryUrl += '&state=';
            queryUrl += query.state;
            this.fetchProperties(queryUrl);
        },
        fetchProperties(url) {
            window.axios.get(url, {}).then((response) => {
                this.updateResults(response.data.properties);
                history.pushState({last: "search"}, "bookMe - Search Results", url)
            }).catch((error) => {
                //TODO - Error handling
                //TODO - Check for validation error, fire window.bookMe.Event.fire('validation-error', {city: ['foo', 'bar'], state: ['baz']})
            });
        }
    }
});
