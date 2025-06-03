import React from "react";

const Loading = () => (
  <div className="d-flex justify-content-center align-items-center" style={{ minHeight: "200px" }}>
    <div className="spinner-border text-primary" role="status">
      <span className="visually-hidden">Loading...</span>
    </div>
  </div>
);

export default Loading;
