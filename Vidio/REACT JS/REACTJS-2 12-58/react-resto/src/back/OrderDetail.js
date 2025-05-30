import React, { useState } from "react";
import useGet from "../hooks/useGet";
import Side from "./Side";
import axios from "axios";
import { link } from "../axios/link";

const OrderDetail = () => {
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");
  const [filteredDetails, setFilteredDetails] = useState(null);
  const { isi: details, loading, error } = useGet("detail");

  const handleFilter = async () => {
    try {
      const response = await axios.get(`${link}/detail/filter`, {
        params: {
          start_date: startDate,
          end_date: endDate,
        },
      });
      setFilteredDetails(response.data);
    } catch (err) {
      console.error("Error filtering details:", err);
    }
  };

  const displayDetails = filteredDetails || details;

  return (
    <>
      <div className="row">
        <div className="col-md-3">
          <Side />
        </div>
        <div className="col-md-9">
          <div className="container mt-4">
            <h2>Detail Penjualan</h2>

            {/* Filter Form */}
            <div className="row mb-3">
              <div className="col-md-3">
                <input type="date" className="form-control" value={startDate} onChange={(e) => setStartDate(e.target.value)} />
              </div>
              <div className="col-md-3">
                <input type="date" className="form-control" value={endDate} onChange={(e) => setEndDate(e.target.value)} />
              </div>
              <div className="col-md-2">
                <button className="btn btn-primary" onClick={handleFilter}>
                  Filter
                </button>
              </div>
            </div>

            {error && <div className="alert alert-danger">{error}</div>}

            <div className="table-responsive">
              <table className="table table-striped table-hover">
                <thead className="table-dark">
                  <tr>
                    <th>No</th>
                    <th>Faktur</th>
                    <th>Tanggal Order</th>
                    <th>Menu</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  {loading ? (
                    <tr>
                      <td colSpan={7} className="text-center">
                        Loading...
                      </td>
                    </tr>
                  ) : displayDetails && displayDetails.length > 0 ? (
                    displayDetails.map((detail, idx) => (
                      <tr key={detail.iddetail}>
                        <td>{idx + 1}</td>
                        <td>{detail.idorder}</td>
                        <td>{detail.tglorder}</td>
                        <td>{detail.menu}</td>
                        <td>{detail.jumlah}</td>
                        <td>Rp. {Number(detail.hargajual).toLocaleString("id-ID")}</td>
                        <td>Rp. {Number(detail.hargajual * detail.jumlah).toLocaleString("id-ID")}</td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={7} className="text-center">
                        Tidak ada data detail order
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default OrderDetail;
