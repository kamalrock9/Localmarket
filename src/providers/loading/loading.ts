import { Injectable } from '@angular/core';
import { LoadingController } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

@Injectable()
export class LoadingProvider {
  load: any;
  constructor(private loader: LoadingController, private translate: TranslateService) { }
  show(dismissOnPageChange=true) {
    /*this.translate.get(['WAIT']).subscribe( x=> {
      this.load = this.loader.create({
        spinner: 'dots'
      });
    });*/
    this.translate.get(["LOADING"]).subscribe(x => {

      this.load = this.loader.create({
        //content: x.LOADING,
        // spinner: 'ios',
        cssClass: 'loading-custom',
        content: `<div class="header">
                  </div>
                  <div class="content">
                    <div class="loader_outer">
                      <div class="loader">
                      </div>
                    </div>
                  </div>`,
        spinner: 'hide',
        dismissOnPageChange: dismissOnPageChange
      });
      return this.load.present();
    });
  }
  
  showWithMessage(msg: string) {
    /*this.translate.get(['WAIT']).subscribe( x=> {
      this.load = this.loader.create({
        spinner: 'dots'
      });
    });*/
    this.load = this.loader.create({
      //content: x.LOADING,
      // spinner: 'ios',
      cssClass: 'loading-custom-msg',
      content: `<div class="header">
                  </div>
                  <div class="content">
                    <div class="loader_outer">
                      <div class="loader"></div></br>
                      `+ msg + `
                    </div>
                  </div>`,
      spinner: 'hide',
      dismissOnPageChange: false
    });
    return this.load.present();
  }

  dismiss() {
    this.load.dismiss();
  }
}
