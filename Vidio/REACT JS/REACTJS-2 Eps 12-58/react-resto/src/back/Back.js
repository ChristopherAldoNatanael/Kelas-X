import React from "react";
import Side from "./Side";
import Content from "./Content";
import { useNavigate } from "react-router-dom";

const Back = () => {
  const navigate = useNavigate();

  return (
    <div className="min-h-screen bg-gray-100">
      <div className="flex">
        {/* Sidebar */}
        <Side />

        {/* Main Content */}
        <div className="flex-1 min-h-screen">
          <div className="p-8">
            <Content />
          </div>
        </div>
      </div>
    </div>
  );
};

export default Back;
