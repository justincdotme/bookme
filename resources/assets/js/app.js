require('./bootstrap');

//Classes
import Event from './Event';
import State from './State';

//Init
window.bookMe = {
    Event: new Event,
    stateManager: new State
};
