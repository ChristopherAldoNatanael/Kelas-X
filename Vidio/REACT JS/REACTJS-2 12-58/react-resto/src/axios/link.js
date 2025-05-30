import axios from "axios";

const baseURL = "http://localhost:8000/api";

const link = "http://localhost:8000/api";
export { link };

// Configure axios defaults
axios.defaults.baseURL = baseURL;
axios.defaults.headers.common["Accept"] = "application/json";
axios.defaults.headers.post["Content-Type"] = "application/json";

// Add response interceptor for error handling
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error("API Error:", error);
    return Promise.reject(error);
  }
);
