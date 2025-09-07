import { login } from './login.js';
import { checkLogin } from './checkLogin.js';
import { refresh } from './refresh.js';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get current directory for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Read credentials JSON file
const credintalsPath = path.resolve(__dirname, 'credintals.json');
const credintals = JSON.parse(fs.readFileSync(credintalsPath, 'utf8'));

const user = credintals.users[Math.floor(Math.random() * credintals.users.length)];
const rade = {
    login: async (mobile = null) => await login(user, mobile),
    checkLogin: async (mobile = null) => await checkLogin(user, mobile),
    refresh: async (mobile = null) => await refresh(user, mobile)
}

export default rade;