import { Events } from 'ionic-angular';

import { AppModule } from '../app/app.module';

export function PageTrack(param?: any): ClassDecorator {
    return function (constructor: any) {
        const ionViewDidEnter = constructor.prototype.ionViewDidEnter;
        constructor.prototype.ionViewDidEnter = function (...args: any[]) {
            const events = AppModule.injector.get(Events);
            let pageName = (param && param.pageName) ? param.pageName : this.constructor.name;
            events.publish('view:enter', pageName);
            ionViewDidEnter && ionViewDidEnter.apply(this, args);
        }
    }
}