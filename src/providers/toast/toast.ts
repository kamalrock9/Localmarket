import { TranslateService } from '@ngx-translate/core';
import { Injectable } from '@angular/core';
import { ToastController } from 'ionic-angular';

@Injectable()
export class ToastProvider {
  toast: any;

  constructor(private _toast: ToastController, public translate: TranslateService) { }

  show(msg: string, pos: string = 'bottom') {
    this._toast.create({
      message: msg,
      duration: 2000,
      position: pos
    }).present();
  }

  showWithClose(msg: string, pos: string = 'bottom') {
    this.translate.get(['OK']).subscribe((x) => {
      this._toast.create({
        message: msg,
        showCloseButton: true,
        position: pos,
        closeButtonText: x.OK
      }).present();
    })
  }
  showError() {
    this.translate.get(['ERROR']).subscribe((x) => {
      this._toast.create({
        message: x.ERROR,
        duration: 2000,
        position: 'bottom'
      }).present();
    })
  }
}
