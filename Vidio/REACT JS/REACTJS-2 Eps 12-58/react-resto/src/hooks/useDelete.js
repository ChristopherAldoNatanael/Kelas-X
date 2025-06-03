import axios from "axios";
import { link } from "../axios/link";

const useDelete = () => {
  const hapusData = async (endpoint, id) => {
    try {
      const response = await axios.delete(`${link}/${endpoint}/${id}`);
      if (!response || response.status !== 200) {
        throw new Error("Server error");
      }
      return true;
    } catch (error) {
      if (error.response) {
        throw new Error(error.response.data.message || "Server error");
      }
      throw new Error("Network error");
    }
  };

  return { hapusData };
};

export default useDelete;
