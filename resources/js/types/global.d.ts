import { route as ziggyRoute } from 'ziggy-js';
import { Config } from 'ziggy-js';
import { User } from './';

declare module '@inertiajs/core' {
    interface InertiaConfig {
        sharedPageProps: {
            auth: {
                user: User;
            };
            ziggy: Config & { location: string };
        };
    }
}

declare global {
    /* eslint-disable no-var */
    var route: typeof ziggyRoute;
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof ziggyRoute;
    }
}
