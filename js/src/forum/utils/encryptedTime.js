import aesjs from 'aes-js';

/**
 * Function to get the current browser time in encrypted format. If submitted with a form,
 * it can be used to detect how long it took a user to fill out a form, detecting bots.
 * @returns {*}
 */
export default function encryptedTime() {
    let bytes = app.data.resources[0].attributes['guardianEncryptionKey'];
    let key = [];

    for (var i = 0; i < bytes.length; i++) {
        key.push(bytes.charCodeAt(i));
    }

    let aesCbc = new aesjs.ModeOfOperation.cbc(key);

    let date = new Date();
    date.setSeconds(date.getSeconds() + 2);

    let textBytes = aesjs.utils.utf8.toBytes(date.getTime() + 'pad');

    let encryptedBytes = aesCbc.encrypt(textBytes);

    return aesjs.utils.hex.fromBytes(encryptedBytes);
}