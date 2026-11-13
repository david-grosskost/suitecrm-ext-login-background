import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {provideHttpClient, withInterceptorsFromDi} from '@angular/common/http';

// login-background: loaded by SuiteCRM 8 when the interface starts (module
// federation, declared by config/extension.php), it puts the image chosen in
// Administration → Fond de la page de connexion behind the login page.
//
// The image comes from a legacy entry point open without login (the login
// page is seen before it): with no image chosen, it answers 404 and the
// browser keeps the page's own background. Only background-image and its
// layout are set, never the colour.
const STYLE_ID = 'login-background';
const IMAGE_URL = 'legacy/index.php?entryPoint=loginBackground';
const CSS = `.login-view {
    background-image: url('${IMAGE_URL}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}`;

@NgModule({
    declarations: [],
    imports: [CommonModule],
    providers: [provideHttpClient(withInterceptorsFromDi())]
})
export class ExtensionModule {
    // SuiteCRM 8.10.2 creates the module (createNgModule) but never calls
    // init(): the work is done here, when it is loaded at start.
    constructor() {
        if (!document.getElementById(STYLE_ID)) {
            const style = document.createElement('style');
            style.id = STYLE_ID;
            style.textContent = CSS;
            document.head.appendChild(style);
        }
        console.info('login-background : chargé, fond de la page de connexion');
    }

    init(): void {
    }
}
