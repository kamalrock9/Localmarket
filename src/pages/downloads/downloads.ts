import { TranslateService } from '@ngx-translate/core';
import { WooCommerceProvider, ToastProvider, LoadingProvider, UserProvider } from './../../providers/providers';
import { Component, NgZone } from '@angular/core';
import { IonicPage, NavController, NavParams, Platform, AlertController } from 'ionic-angular';
import { File } from '@ionic-native/file';
import { FileTransfer, FileTransferObject } from '@ionic-native/file-transfer';
import { AndroidPermissions } from '@ionic-native/android-permissions';


@IonicPage({
  priority: 'low'
})
@Component({
  selector: 'page-downloads',
  templateUrl: 'downloads.html',
})
export class DownloadsPage {
  downloads: any;
  storageDirectory: string = '';
  progress: any;
  constructor(public navCtrl: NavController, public navParams: NavParams, public WC: WooCommerceProvider,
    public platform: Platform, public toast: ToastProvider, private file: File, private transfer: FileTransfer,
    private alertCtrl: AlertController, private zone: NgZone, private androidPermissions: AndroidPermissions,
    private loader: LoadingProvider, public user: UserProvider, public translate: TranslateService) {

    this.WC.getDownloads(user.id).then((data) => {
      console.log(data);
      this.zone.run(() => {
        this.downloads = data;
      });
    }, (err) => {
      toast.showError();
    });


    if (!this.platform.is('cordova')) {
      this.translate.get(['ONLY_DEVICE']).subscribe(x => {
        this.toast.show(x.ONLY_DEVICE);
        return;
      });
    }

    if (this.platform.is('ios')) {
      this.storageDirectory = this.file.documentsDirectory;
    }
    else if (this.platform.is('android')) {
      this.storageDirectory = this.file.externalRootDirectory;
    }

  }

  ionViewDidLoad() {
    console.log('ionViewDidLoad DownloadsPage');
  }
  checkPermissionAndDownload(url, fileName) {
    if (!this.platform.is('cordova')) {
      this.translate.get(['ONLY_DEVICE', 'ONLY_DEVICE_DESC', 'OK']).subscribe(x => {
        this.alertCtrl.create({
          title: x.ONLY_DEVICE,
          message: x.ONLY_DEVICE_DESC,
          buttons: [{
            text: x.OK
          }]
        }).present();
      });
      return;
    }
    if (this.platform.is('android')) {
      this.androidPermissions.checkPermission(this.androidPermissions.PERMISSION.WRITE_EXTERNAL_STORAGE).then(
        result => {
          console.log('Has permission?', result.hasPermission);
          if (result.hasPermission) {
            this.downloadNow(url, fileName);
          }
        },
        err => {
          this.androidPermissions.requestPermission(this.androidPermissions.PERMISSION.WRITE_EXTERNAL_STORAGE)
        }
      );
    } else {
      this.downloadNow(url, fileName);
    }

  }
  downloadNow(url, fileName) {
    const fileTransfer: FileTransferObject = this.transfer.create();
    this.loader.show();
    this.translate.get(['OK', 'DOWNLOAD_SUCCESS', 'DOWNLOAD_FAILED']).subscribe((x) => {
      fileTransfer.download(url, this.storageDirectory + fileName).then((entry) => {
        this.loader.dismiss();
        this.alertCtrl.create({
          title: x.DOWNLOAD_SUCCESS,
          subTitle: fileName + ' was successfully downloaded to: ' + entry.toURL(),
          buttons: [x.OK]
        }).present();

      }, (error) => {
        // handle error
        this.loader.dismiss();
        this.alertCtrl.create({
          title: x.DOWNLOAD_FAILED,
          subTitle: fileName + ' was not downloaded. Error code: ' + error.code,
          buttons: [x.OK]
        }).present();
      });
      fileTransfer.onProgress((progress) => {
        this.progress = progress;
      });

    });

  }
}
