/** localStorage wrapper that never throws (private mode, blocked storage). */
export const storage = {
    get(key) {
        try {
            return localStorage.getItem(key);
        } catch {
            return null;
        }
    },
    set(key, value) {
        try {
            if (value === null) localStorage.removeItem(key);
            else localStorage.setItem(key, value);
        } catch {
            /* storage unavailable */
        }
    },
};
