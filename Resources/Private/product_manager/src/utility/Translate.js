/*global TYPO3*/

class Translate
{
    /**
     * Translate TYPO3 labels
     *
     * @param key
     * @returns {string}
     */
    static translate(key) {
        key = 'js.' + key;
        return TYPO3['lang'][key] || key;
    }
}

export default Translate;
