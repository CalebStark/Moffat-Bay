package com.example.moffatbay.servlets;

import java.io.IOException;
import java.io.PrintWriter;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import com.google.gson.JsonObject;

@WebServlet("/LookupServlet")
public class LookupServlet extends HttpServlet {

    private static final String DB_URL = "jdbc:mysql://localhost:3306/moffat_bay_marina?useSSL=false&serverTimezone=UTC";
    private static final String DB_USER = "root";
    private static final String DB_PASSWORD = "";

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        response.setContentType("application/json");
        PrintWriter out = response.getWriter();

        String lookupValue = request.getParameter("lookupValue");

        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
            Connection con = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);

           //Should get it by email only
            String sql = "SELECT c.boatName, c.boatLength, r.checkInDate , s.slipSize FROM Reservations r JOIN Customers c ON r.customerId = c.customerId JOIN Slips s ON r.slipId = s.slipId WHERE c.email = ?";

            PreparedStatement pst = con.prepareStatement(sql);
            pst.setString(1, lookupValue);
            ResultSet rs = pst.executeQuery();

            if (rs.next()) {
                JsonObject jsonOutput = new JsonObject();
                jsonOutput.addProperty("boatName", rs.getString("boatName"));
                jsonOutput.addProperty("boatLength", rs.getInt("boatLength"));
                jsonOutput.addProperty("checkInDate", rs.getString("checkInDate"));
                jsonOutput.addProperty("slipSize", rs.getString("slipSize"));  //Missing information added

                out.print(jsonOutput.toString());
                out.flush();
            } else {
                response.setStatus(404); // Not Found
                JsonObject jsonOutput = new JsonObject();
                jsonOutput.addProperty("message", "Reservation not found.");
                out.print(jsonOutput.toString());
                out.flush();
            }

            con.close();

        } catch (ClassNotFoundException | SQLException e) {
            System.err.println(e.getMessage());
            response.setStatus(500); // Internal Server Error
            JsonObject jsonOutput = new JsonObject();
            jsonOutput.addProperty("message", "Error looking up reservation.");
            out.print(jsonOutput.toString());
            out.flush();
        }
    }
}