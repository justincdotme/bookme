require('./bootstrap');

//Classes
import Event from './Event';
import State from './State';

//Components
import PropertyPreview from './components/property-preview.vue';
import SearchPaginator from './components/search-paginator.vue';
import SearchWidget from './components/search-widget.vue';

//Init
window.bookMe = {
    Event: new Event,
    stateManager: new State
};
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
        getSearchParams() {
            let output = [], query;
            let queries = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');

            for(let i=0; i<queries.length; i++)
            {
                query = queries[i].split('=');
                if (query[0] != 'page') {
                    output[query[0]] = query[1];
                }
            }
            return output;
        },
        updateList() {
            this.properties = this.getProperties(); //Because I can't make the properties prop reactive for some reason.
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
            //TODO - Convert query to URL

        },
        fetchProperties(url) {
            //TODO - Implement actual search query
            window.axios.get(url, {}).then((response) => {
                this.updateResults(response.data.properties);
                history.pushState({last: "search"}, "bookMe - Search Results", url)
            }).catch((error) => {
                //TODO - Check for valiation error, fire window.bookMe.Event.fire('validation-error', {city: ['foo', 'bar'], state: ['baz']})
            });
        }
    }
});
