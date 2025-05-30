import { useState, useEffect } from "react";
import axios from "axios";
import { link } from "../axios/link";

const useGet = (endpoint) => {
  const [isi, setIsi] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchData = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${link}/${endpoint}`);

      if (response.data.data) {
        setIsi(response.data.data);
      } else if (Array.isArray(response.data)) {
        setIsi(response.data);
      } else {
        setIsi([]);
      }
      setError(null);
    } catch (err) {
      console.error("Error fetching data:", err);
      setError(err.message);
      setIsi([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, [endpoint]);

  return { isi, loading, error, fetchData };
};

export default useGet;
