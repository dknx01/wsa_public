import { startStimulusApp } from '@symfony/stimulus-bundle';
import Notification from '@stimulus-components/notification'
import Lucide from 'Lucide';

Lucide.initialize(null)
// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp();
app.register('notification', Notification)

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
